import Rest from 'js/Services/Rest';
import {objectToUrl} from 'js/Services/Utils';


const list = (options) => {
    options = options || {};
    return Rest.authRequest('/roles?'+objectToUrl(options),'GET');
};

const selectList = () => {
   return new Promise((resolve,reject) => {
       list().then((roles) => {
           let output = {};
           roles['roles'].forEach((role) => {
               output[role.id] = role.label;
           });

           resolve(output);
       },reject);
   });
};


const add = (role) => {
    return Rest.authRequest('/roles','POST',role);
};

const deleteItem = (id) => {
    return Rest.authRequest('/roles/'+id,'DELETE');
};

const getOne = (id) => {
    return Rest.authRequest('/roles/'+id,'GET');
};

const getAllPermissions = () => {
    return Rest.authRequest('/roles/allpermissions/','GET');
};

const addPermission = (idRole,resource,right) => {
    return Rest.authRequest('/roles/'+idRole+'/permissions','POST', {resource, right});
};

const deletePermission = (idRole,resource,right) => {
    return Rest.authRequest('/roles/'+idRole+'/permissions/'+resource+'.'+right,'DELETE');
};



export {
    list,
    selectList,
    add,
    deleteItem,
    getOne,
    getAllPermissions,
    addPermission,
    deletePermission
}
