import Rest from 'js/Services/Rest';
import {objectToUrl} from 'js/Services/Utils';


const list = (options) => {
    options = options || {};
    return Rest.authRequest('/modules?'+objectToUrl(options),'GET');
};

const getOne = (name) => {
    return Rest.authRequest('/modules/'+name,'GET');
};


export {
    list,
    getOne,
}
