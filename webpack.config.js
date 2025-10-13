const path = require('path');
module.exports = {
    entry: './admin/js/main.js',
    output: {
        filename: 'bundle.js',
        path: path.resolve(__dirname, 'admin/js'),
    },
    module: {
        rules: [
            {
                test: /\.css$/i,
                use: ['style-loader', 'css-loader'],
            },
        ],
    },
};
