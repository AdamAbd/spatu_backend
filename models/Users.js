module.exports = (sequelize, DataTypes) => {
  const Users = sequelize.define(
    // "User" means models name
    "Users", {
    id: {
      allowNull: false,
      primaryKey: true,
      type: DataTypes.UUID,
      defaultValue: DataTypes.UUIDV4
    },
    username: {
      allowNull: false,
      type: DataTypes.STRING,
    },
    email: {
      allowNull: false,
      type: DataTypes.STRING,
      validate: {
        isEmail: true,
      },
    },
    password: {
      allowNull: false,
      type: DataTypes.TEXT,
    },
    avatar: {
      allowNull: true,
      type: DataTypes.TEXT,
    },
    google_id: {
      allowNull: true,
      type: DataTypes.TEXT,
    },
    role: {
      allowNull: false,
      type: DataTypes.ENUM('user', 'admin'),
      defaultValue: 'user',
    },
    verified_email: {
      allowNull: true,
      type: DataTypes.DATE,
    },
  }, {
    defaultScope: {
      attributes: { exclude: ['password', 'google_id', 'deleted_at', 'refresh_token'] },
    },

    scopes: {
      withPassword: {
        attributes: { exclude: ['google_id', 'deleted_at', 'refresh_token'] },
      },
      withGoogleId: {
        attributes: { exclude: ['password', 'deleted_at', 'refresh_token'] },
      },
    },

    // add the timestamp attributes (updatedAt, createdAt)
    timestamps: true,

    // don't delete database entries but set the newly added attribute deletedAt
    // to the current date (when deletion was done). paranoid will only work if
    // timestamps are enabled
    // paranoid: true,

    // don't use camelcase for automatically added attributes but underscore style
    // so updatedAt will be updated_at
    underscored: true,

    // disable the modification of tablenames; By default, sequelize will automatically
    // transform all passed model names (first parameter of define) into plural.
    // if you don't want that, set the following
    // freezeTableName: true,

    // define the table's name
    tableName: 'users'
  }
  );
  return Users;
};