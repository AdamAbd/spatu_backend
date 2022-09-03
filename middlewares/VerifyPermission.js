require("dotenv").config();

const jwt = require('jsonwebtoken');
const { Users } = require('../models');

const { ACCES_TOKEN_SECRET } = process.env;

module.exports = (...roles) => {
    return (req, res, next) => {
        const authorizationHeader = req.headers.authorization;
        const token = authorizationHeader && authorizationHeader.split(' ');

        if (token == null || token[0] !== 'Bearer') return res.status(401).json({ message: 'UNAUTHORIZED' });

        jwt.verify(token[1], ACCES_TOKEN_SECRET, async (err, decoded) => {
            if (err) return res.status(403).json({ message: 'FORBIDDEN' });

            const user = await Users.findByPk(decoded.id);

            if (!roles.includes(user.role)) return res.status(405).json({ message: 'You don\'t have any permission' });

            req.user = user;

            next();
        });
    }
}