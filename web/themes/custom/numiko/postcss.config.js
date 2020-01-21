/* eslint-env node */
module.exports = ({ env }) => ({
    parser: 'postcss-scss',
    plugins: {
      'postcss-preset-env': {}, // This includes autoprefixer
      'cssnano': env === 'production' ? {} : false
    }
});