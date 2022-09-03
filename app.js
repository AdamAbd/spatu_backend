require("dotenv").config();

/// Import Packages
const express = require('express');
const path = require('path');
const cookieParser = require('cookie-parser');
const logger = require('morgan');
const fileUpload = require('express-fileupload');
const cors = require('cors');

/// Import Helpers
const responseHelpers = require('./helpers/ResponseHelper').helper;

/// Import Routers
const indexRouter = require('./routes/index');
const usersRouter = require('./routes/UsersRoute');

const app = express();

app.use(logger('dev'));
app.use(express.json());
app.use(express.urlencoded({ extended: true }));
app.use(cookieParser());
app.use(express.static(path.join(__dirname, 'public')));
app.use(fileUpload());
app.use(cors());
app.use(responseHelpers());

app.use('/halo', indexRouter);
app.use('/api/v1/users', usersRouter);

module.exports = app;
