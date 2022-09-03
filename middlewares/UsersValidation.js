const Joi = require('joi');

const validateRegister = (req, res, next) => {
    const schema = Joi.object({
        username: Joi.string().min(6).max(12).required(),
        email: Joi.string().email({ minDomainSegments: 2, tlds: { allow: ["com", "id"] } }).required(),
        password: Joi.string().min(8).required(),
    });

    const { error, value } = schema.validate(req.body);
    if (error) return res.failValidationError(error.message);
    req.registerValues = value;

    next();
}

const validateLogin = (req, res, next) => {
    const schema = Joi.object({
        email: Joi.string().email({ minDomainSegments: 2, tlds: { allow: ["com", "id"] } }),
        password: Joi.string().min(8).required(),
    });

    const { error, value } = schema.validate(req.body);
    if (error) return res.failValidationError(error.message);
    req.loginValues = value;

    next();
}

module.exports = {
    validateRegister,
    validateLogin,
};