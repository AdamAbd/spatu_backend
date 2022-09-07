const express = require('express');
const nodemailer = require('nodemailer');

const router = express.Router();

/* GET home page. */
router.get('/', function (req, res, next) {
  const client = nodemailer.createTransport({
    service: "Gmail",
    auth: {
      user: "sequelacc@gmail.com",
      pass: "lbrzlisciqbzeooa"
    }
  });

  client.sendMail(
    {
      from: "sequelacc@gmail.com",
      to: "adam2802002@gmail.com",
      subject: "Sending it from Heroku",
      text: "Hey, I'm being sent from the cloud"
    },
    (err, data) => {
      if (err) {
        return res.json({ data: err })
      }
      return res.json({ data: data })
    }
  )

});

module.exports = router;
