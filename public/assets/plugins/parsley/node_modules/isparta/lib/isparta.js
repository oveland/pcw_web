'use strict';

Object.defineProperty(exports, '__esModule', {
	value: true
});

function _interopRequireWildcard(obj) { if (obj && obj.__esModule) { return obj; } else { var newObj = {}; if (obj != null) { for (var key in obj) { if (Object.prototype.hasOwnProperty.call(obj, key)) newObj[key] = obj[key]; } } newObj['default'] = obj; return newObj; } }

var _istanbul = require('istanbul');

var istanbul = _interopRequireWildcard(_istanbul);

for (var key in istanbul) {
	if (istanbul.hasOwnProperty(key)) {
		exports[key] = istanbul[key];
	}
}

var _instrumenter = require('./instrumenter');

Object.defineProperty(exports, 'Instrumenter', {
	enumerable: true,
	get: function get() {
		return _instrumenter.Instrumenter;
	}
});

var _packageJson = require('../package.json');

Object.defineProperty(exports, 'version', {
	enumerable: true,
	get: function get() {
		return _packageJson.version;
	}
});