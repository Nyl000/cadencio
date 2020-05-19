import Rest from 'js/Services/Rest';
import {objectToUrl} from 'js/Services/Utils';


const list = (options) => {
    options = options || {};
    return Rest.authRequest('/modules?'+objectToUrl(options),'GET');
};

const getOne = (name) => {
    return Rest.authRequest('/modules/'+name,'GET');
};

const getActivesModules = () => {
	let activeModules = JSON.parse(localStorage.getItem('active_modules'));
	return activeModules || [];

};

const refreshActivesModules = (callback) => {
	list({nbItems : 9999999}).then((modulesResponse) => {
		let activeModules = [];
		modulesResponse.modules.forEach((mod) => {
			if (mod.active == 1) {
				activeModules.push(mod.name);
			}
		});
		localStorage.setItem('active_modules', JSON.stringify(activeModules))
        if (typeof callback === 'function') {
			callback();
        }
	})
};

export {
    list,
    getOne,
    getActivesModules,
    refreshActivesModules,
}
