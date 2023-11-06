const plugin = require('tailwindcss/plugin');

module.exports = {
  theme: {
    extend: {
      maxWidth: {
        '8xl': '90rem',
      },
      backgroundImage: {
        'avatar': "url('/../../../modules/custom/server_style_guide/assets/Avatar.png')",
        'avatar-cody': "url('/../../../modules/custom/server_style_guide/assets/Avatar-cody.png')",
        'avatar-courtney': "url('/../../../modules/custom/server_style_guide/assets/Avatar-courtney.png')",
        'avatar-esther': "url('/../../../modules/custom/server_style_guide/assets/Avatar-esther.png')",
        'avatar-jenny': "url('/../../../modules/custom/server_style_guide/assets/Avatar-jenny.png')",
        'avatar-kristin': "url('/../../../modules/custom/server_style_guide/assets/Avatar-kristin.png')",
        'avatar-theresa': "url('/../../../modules/custom/server_style_guide/assets/Avatar-theresa.png')",
        'avatar-cameron': "url('/../../../modules/custom/server_style_guide/assets/Avatar-cameron.png')",
      },
    },
    fontFamily: {
      'headers': ["Roboto", 'sans-serif'],
      'body': ["Open Sans", 'sans-serif'],
    }
  },
  content: [
    // Look in the twig files.
    './templates/**/*.html.twig',
    // Look in the js files.
    './src/js/**/*.js',
    // A preprocess function might inject a class.
    './server_theme.theme',
    // Custom module and the Style guide may have needed classes.
    '../../../modules/custom/**/*.php',
    '../../../modules/custom/**/*.html.twig',
  ],
  safelist: [
    // Add here custom class names.
    // https://tailwindcss.com/docs/content-configuration#safelisting-classes
  ],
  plugins: [
    require('@tailwindcss/forms'),
    require('@tailwindcss/typography'),
  ],
};
