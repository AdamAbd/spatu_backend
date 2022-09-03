module.exports = (sequelize, DataTypes) => {
  const VerifyTokens = sequelize.define(
    // "User" means models name
    "VerifyTokens", {
    id: {
      allowNull: false,
      primaryKey: true,
      type: DataTypes.UUID,
      defaultValue: DataTypes.UUIDV4
    },
    user_id: {
      allowNull: false,
      references: { model: 'users', key: 'id' },
      type: DataTypes.UUID,
    },
    token: {
      type: DataTypes.INTEGER,
      validate: {
        len: {
          args: [0, 6],
        }
      }
    },
    expired_at: {
      allowNull: false,
      type: DataTypes.DATE,
    },
  }, {
    // add the timestamp attributes (updatedAt, createdAt)
    timestamps: true,

    // remove updatedAt
    updatedAt: false,

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
    tableName: 'verify_tokens'
  }
  );
  return VerifyTokens;
};