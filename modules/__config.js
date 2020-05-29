const fs = require('fs');


const getModulesFiles = function() {
    var out = [];
    let files = fs.readdirSync('../modules');

        files.forEach((file) => {
            if (fs.lstatSync('../modules/' + file).isDirectory()) {
                let moduleDir = '../modules/' + file;
                if (fs.existsSync(moduleDir + '/front/index.js')) {
                    if (fs.lstatSync(moduleDir + '/front/index.js').isFile()) {
                        out.push(moduleDir + '/front/index.js');
                    }
                }
            }
        });

    return out;
};

const getModulesDirs = function() {
    var out = [];
    let files = fs.readdirSync('../modules');

    files.forEach((file) => {
        if (fs.lstatSync('../modules/' + file).isDirectory()) {
            let moduleDir = '../modules/' + file;
            if (fs.existsSync(moduleDir + '/front')) {
                if (fs.lstatSync(moduleDir + '/front').isDirectory()) {
                    out.push(moduleDir + '/front');
                }
            }
			if (fs.existsSync(moduleDir + '/front/node_modules')) {
				if (fs.lstatSync(moduleDir + '/front/node_modules').isDirectory()) {
					out.push(moduleDir + '/front/node_modules');
				}
			}
        }
    });

    return out;
};

const getResourcesDirs = function() {
    var out = [];
	let files = fs.readdirSync('../modules');
    files.forEach((file) => {
		if (fs.lstatSync('../modules/' + file).isDirectory()) {
			let moduleDir = '../modules/' + file;
			if (fs.existsSync(moduleDir + '/front/resources')) {
				if (fs.lstatSync(moduleDir + '/front/resources').isDirectory()) {
				    let resDir = moduleDir + '/front/resources';
					out.push({from : resDir, to: 'resources/'+file});
				}
			}
		}
	});

	return out;


};

exports.getModulesFiles = getModulesFiles;
exports.getModulesDirs = getModulesDirs;
exports.getResourcesDirs = getResourcesDirs;