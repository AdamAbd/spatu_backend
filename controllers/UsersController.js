require("dotenv").config();

const bcrypt = require('bcrypt');
const jwt = require('jsonwebtoken');
const { Op } = require("sequelize");
const nodemailer = require('nodemailer');
const moment = require('moment');

const { Users, VerifyCodes, RefreshToken } = require('../models');

const { ACCES_TOKEN_SECRET, REFRESH_TOKEN_SECRET, MAIL_USERNAME, MAIL_PASSWORD, MAIL_FROM_ADDRESS } = process.env;

const register = async (req, res) => {
    try {
        // Get all validated values from middlewares
        const { username, email, password } = req.registerValues;

        // Check if email is exist or not
        const emailExist = await Users.findOne({ where: { email: email } });
        if (emailExist) return res.failValidationError('Email already used');

        // Hash password
        const hashPassword = await bcrypt.hash(password, 10);

        // Store user data to the database
        const user = await Users.create({ username: username, email: email, password: hashPassword });

        await sendVerifyCode(res, user.id, email);
    } catch (error) {
        console.warn(error.message);
        return res.failServerError(error.message);
    }
}

const verify = async (req, res) => {
    try {
        // Get all validated values from middlewares
        const { code } = req.verifyValues;

        const verifyCodeExist = await VerifyCodes.findOne({ where: { code: code, expired_at: { [Op.gte]: moment() } } });
        if (!verifyCodeExist) return res.failUnauthorized();

        await Users.update({ verified_email: moment() }, { where: { id: verifyCodeExist.user_id } });

        await verifyCodeExist.destroy();

        return res.respond("Your email verification success")
    } catch (error) {
        console.warn(error.message);
        return res.failServerError(error.message);
    }
}

const resendCode = async (req, res) => {
    try {
        // Get all validated values from middlewares
        const { email } = req.resendCodeValues;

        // Check if email is exist or not
        const userExist = await Users.findOne({ where: { email: email, verified_email: null } });
        if (!userExist) return res.failUnauthorized();

        // Delete existing verify code
        await VerifyCodes.destroy({ where: { user_id: userExist.id, } });

        await sendVerifyCode(res, userExist.id, email);
    } catch (error) {
        console.warn(error.message);
        return res.failServerError(error.message);
    }
}

const login = async (req, res) => {
    try {
        const { email, password } = req.loginValues;

        const userExist = await Users.scope('withPassword').findOne({ where: { email: email, verified_email: { [Op.not]: null } } });
        if (!userExist) return res.failValidationError('Credential Error');

        const matchPassword = await bcrypt.compare(password, userExist.password);
        if (!matchPassword) return res.failValidationError('Credential Error');

        const accessToken = jwt.sign({
            iss: "spatu",
            context: {
                user: {
                    id: userExist.id,
                    email: userExist.email,
                },
                roles: "user"
            },
        }, ACCES_TOKEN_SECRET, {
            expiresIn: '30m',
        });

        const refreshToken = jwt.sign({
            iss: "spatu",
            context: {
                user: {
                    id: userExist.id,
                    email: userExist.email,
                },
                roles: "user"
            },
        }, REFRESH_TOKEN_SECRET, {
            expiresIn: '1d',
        });

        await RefreshToken.create({ user_id: userExist.id, token: refreshToken });

        res.cookie('spatu_token', refreshToken, {
            httpOnly: true,
            maxAge: 24 * 60 * 60 * 1000,
        });

        return res.respond({
            user: {
                id: userExist.id,
                username: userExist.username,
                email: userExist.email,
                created_at: userExist.created_at,
                updated_at: userExist.updated_at,
            },
            access_token: accessToken,
        });
    } catch (error) {
        console.warn(error);
        return res.failServerError(error.message);
    }
}

const sendVerifyCode = async (res, userId, email) => {
    // Creating the verify token
    const generateCode = Math.floor(100000 + Math.random() * 900000);
    // Creating the expire date time using moment js
    const expire = moment().add(1, "minute");

    //! Need improvement
    // Store verify token data to the database
    await VerifyCodes.create({ user_id: userId, code: generateCode, expired_at: expire });

    // Initialize nodemailer
    const client = nodemailer.createTransport({
        service: "Gmail",
        auth: {
            user: MAIL_USERNAME,
            pass: MAIL_PASSWORD
        }
    });

    // Sending the mail
    client.sendMail(
        {
            from: MAIL_FROM_ADDRESS,
            to: email,
            subject: "Spatu Email Verification",
            text: `This is your code for email verification: ${generateCode}`
        },
        (err, data) => {
            if (err) {
                return res.failServerError(err);
            }
            return res.respondCreated('Please check your email');
        }
    );
}

module.exports = { register, verify, resendCode, login }