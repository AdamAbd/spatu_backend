'use strict';
module.exports = {
  async up(queryInterface, Sequelize) {
    await queryInterface.createTable('verify_codes', {
      id: {
        allowNull: false,
        primaryKey: true,
        type: Sequelize.UUID,
        defaultValue: Sequelize.UUIDV4
      },
      user_id: {
        allowNull: false,
        references: { model: 'users', key: 'id' },
        type: Sequelize.UUID,
      },
      code: {
        type: Sequelize.INTEGER,
        len: {
          args: [0, 6]
        },
        unique: true
      },
      expired_at: {
        allowNull: false,
        type: Sequelize.DATE,
      },
      created_at: {
        allowNull: false,
        type: Sequelize.DATE,
      },
    });
  },
  async down(queryInterface, Sequelize) {
    await queryInterface.dropTable('verify_codes');
  }
};