import Rest from 'js/Services/Rest';
import {objectToUrl} from 'js/Services/Utils';

const list = (options) => {
    options = options || {};
    return Rest.authRequest('/planning?'+objectToUrl(options),'GET');
};

const add = (status) => {
    return Rest.authRequest('/planning','POST',status);
};

const deleteItem = (id) => {
    return Rest.authRequest('/planning/'+id,'DELETE');
};

const view = (id) => {
    return Rest.authRequest('/planning/'+id,'GET');

};


export {
    list,
    add,
    deleteItem,
    view
}
