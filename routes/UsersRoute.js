const express = require('express');
const router = express.Router();

const verifyPermissionTo = require('../middlewares/VerifyPermission');
const { validateRegister, validateVerify, validateLogin } = require('../middlewares/UsersValidation');

const { register, verify, getUser, logout, consumeAPI } = require('../controllers/UsersController');

/// All user routes
router.post('/register', validateRegister, register);
router.post('/verify', validateVerify, verify);

module.exports = router;
