const express = require('express');
const router = express.Router();

const verifyPermissionTo = require('../middlewares/VerifyPermission');
const { validateRegister, validateVerify, validateResendCode, validateLogin } = require('../middlewares/UsersValidation');

const { register, verify, resendCode, login, getDetail, logout, consumeAPI } = require('../controllers/UsersController');

/// All user routes
router.post('/register', validateRegister, register);
router.post('/verify', validateVerify, verify);
router.post('/resend_code', validateResendCode, resendCode);
router.post('/login', validateLogin, login);
router.get('/detail', verifyPermissionTo('user'), getDetail);

module.exports = router;
