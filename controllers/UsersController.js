require("dotenv").config();

const bcrypt = require('bcrypt');
const jwt = require('jsonwebtoken');
const { Op } = require("sequelize");
const nodemailer = require('nodemailer');
const moment = require('moment');

const { Users, VerifyTokens } = require('../models');

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

        // Creating the verify token
        const verifyToken = Math.floor(100000 + Math.random() * 900000);
        // Creating the expire date time using moment js
        const expire = moment().add(1, "h");

        //! Need improvement
        // Store verify token data to the database
        await VerifyTokens.create({ user_id: user.id, token: verifyToken, expired_at: expire });

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
                text: `This is your code for email verification: ${verifyToken}`
            },
            (err, data) => {
                if (err) {
                    return res.failServerError(err);
                }
                return res.respond('Please check your email');
            }
        );
    } catch (error) {
        console.warn(error.message);
        return res.failServerError(error.message);
    }
}

const verify = async (req, res) => {
    try {
        // Get all validated values from middlewares
        const { token } = req.verifyValues;

        const verifyTokenExist = await VerifyTokens.findOne({ where: { token: token, expired_at: { [Op.gte]: moment() } } });
        if (!verifyTokenExist) return res.failUnauthorized();

        await Users.update({ verified_email: moment() }, { where: { id: verifyTokenExist.user_id } });

        await verifyTokenExist.destroy();

        return res.respond("Your email verification success")
    } catch (error) {
        console.warn(error.message);
        return res.failServerError(error.message);
    }
}

module.exports = { register, verify }