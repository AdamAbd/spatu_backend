const express = require('express');
const router = express.Router();

const verifyPermissionTo = require('../middlewares/VerifyPermission');
const { validateRegister, validateLogin } = require('../middlewares/UsersValidation');

const { login, register, getUser, logout, consumeAPI } = require('../controllers/UsersController');

/* GET users listing. */
router.post('/register', validateRegister, register);

module.exports = router;
