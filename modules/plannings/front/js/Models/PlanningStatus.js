import Rest from 'js/Services/Rest';
import {objectToUrl} from 'js/Services/Utils';

const list = (options) => {
    options = options || {};
    return Rest.authRequest('/planning_status?'+objectToUrl(options),'GET');
};


const selectList = () => {
    return new Promise((resolve,reject) => {
        list().then((statuses) => {
            let output = {};
            statuses['planning_status'].forEach((status) => {
                output[status.id] = status.title;
            });

            resolve(output);
        },reject);
    });
};

const selectWithColor = () => {
    return new Promise((resolve,reject) => {
        list().then((statuses) => {
            let output = {};
            statuses['planning_status'].forEach((status) => {
                output[status.id] = {title :status.title, color: status.color};
            });

            resolve(output);
        },reject);
    });
};


const add = (status) => {
    return Rest.authRequest('/planning_status','POST',status);
};

const deleteItem = (id) => {
    return Rest.authRequest('/planning_status/'+id,'DELETE');
};


export {
    list,
    add,
    deleteItem,
    selectList,
    selectWithColor
}
