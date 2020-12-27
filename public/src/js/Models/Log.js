import Rest from 'js/Services/Rest';
import {objectToUrl} from 'js/Services/Utils';


const list = (options) => {
    options = options || {};
    return Rest.authRequest('/logs?'+objectToUrl(options),'GET');
};

const getOne = (id) => {
    return Rest.authRequest('/logs/'+id,'GET');
};

export {
    list,
    getOne,
}
