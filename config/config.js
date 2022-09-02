require("dotenv").config();

const { DB_HOSTNAME, DB_PASSWORD, DB_USERNAME, DB_NAME, DB_DIALECT } = process.env;

module.exports = {
  development: {
    username: DB_USERNAME,
    password: DB_PASSWORD,
    database: DB_NAME,
    host: DB_HOSTNAME,
    dialect: DB_DIALECT,
    dialectOptions: {
      useUTC: false, // for reading from database
    },
    timezone: '+07:00', // for writing to database
    define: {
      timestamps: true,
      underscored: true,
      underscoredAll: true,
      createdAt: "created_at",
      updatedAt: "updated_at",
      deletedAt: "deleted_at",
    },
  },
  test: {
    username: DB_USERNAME,
    password: DB_PASSWORD,
    database: DB_NAME,
    host: DB_HOSTNAME,
    dialect: DB_DIALECT,
    dialectOptions: {
      useUTC: false, // for reading from database
    },
    timezone: '+07:00', // for writing to database
    define: {
      timestamps: true,
      underscored: true,
      underscoredAll: true,
      createdAt: "created_at",
      updatedAt: "updated_at",
      deletedAt: "deleted_at",
    },
  },
  production: {
    username: DB_USERNAME,
    password: DB_PASSWORD,
    database: DB_NAME,
    host: DB_HOSTNAME,
    dialect: DB_DIALECT,
    dialectOptions: {
      useUTC: false, // for reading from database
    },
    timezone: '+07:00', // for writing to database
    define: {
      timestamps: true,
      underscored: true,
      underscoredAll: true,
      createdAt: "created_at",
      updatedAt: "updated_at",
      deletedAt: "deleted_at",
    },
  },
};