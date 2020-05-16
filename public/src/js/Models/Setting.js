import Rest from 'js/Services/Rest';
import {objectToUrl} from 'js/Services/Utils';

const get = (name) => {
    return Rest.authRequest('/settings/'+name,'GET');
};

const set = (name,val) => {
	return Rest.authRequest('/settings','POST',{name,val});
};

const sendTestmail = () => {
	return Rest.authRequest(('/settings/sendtestmail'));
};

export {
    get,
	set,
	sendTestmail
}
