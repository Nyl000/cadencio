import Rest from 'js/Services/Rest';
import {objectToUrl} from 'js/Services/Utils';

const list = (options) => {
    options = options || {};
    return Rest.authRequest('/planning_entry?'+objectToUrl(options),'GET');
};

const add = (entry) => {
    return Rest.authRequest('/planning_entry','POST',entry);
};

const deleteItem = (id) => {
    return Rest.authRequest('/planning_entry/'+id,'DELETE');
};

const listByPlanning = (idPlanning, options) => {
    options = options || {};
    return Rest.authRequest('/planning/entries/'+idPlanning+'?'+objectToUrl(options),'GET');
};

const getTimelineByPlanning = (idPlanning, options) => {
    options = options || {};
    return Rest.authRequest('/planning/timeline/'+idPlanning+'?'+objectToUrl(options),'GET');
};

const toggleFollow = (id_entry, id_user) => {

    return Rest.authRequest('/planning_entry/togglefollow', 'POST', {
        id_user : id_user,
        id_entry : id_entry
    });

};

const massUpdate = (ids, key, value) => {
    return Rest.authRequest('/planning_entry/multiples','POST', {ids,key,value});
};

const update = (id_entry, datas) => {
    return Rest.authRequest('/planning_entry/'+id_entry,'POST',datas);
};


export {
    list,
    add,
    deleteItem,
    listByPlanning,
    getTimelineByPlanning,
    toggleFollow,
    update,
    massUpdate
}
