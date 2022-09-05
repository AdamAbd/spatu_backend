module.exports = (sequelize, DataTypes) => {
  const RefreshToken = sequelize.define(
    // "RefreshToken" means models name
    "RefreshToken", {
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
      allowNull: false,
      type: DataTypes.TEXT
    },
  }, {
    defaultScope: {
      attributes: { exclude: ['deleted_at'] },
    },
    timestamps: true,
    paranoid: true,
    underscored: true,
    tableName: 'refresh_tokens'
  }
  );
  return RefreshToken;
};