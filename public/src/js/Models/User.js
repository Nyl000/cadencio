import Rest from 'js/Services/Rest';
import {objectToUrl} from 'js/Services/Utils';

const userOptionsCached = {

};

const testToken = () => {

    let token = localStorage.getItem('token');
    return Rest.authRequest('/user/checktoken', 'GET');
};

const hasPermission = (resource, action) => {
    let user = JSON.parse(localStorage.getItem('user'));
    if (!user) {
        return false;
    }
    return user.role.permissions.indexOf('*.*') >= 0 || user.role.permissions.indexOf(resource + '.*') >= 0 || user.role.permissions.indexOf(resource + '.' + action) >= 0;
};

/**
 * @deprecated Use getUserOptionasync
 * @param key
 * @returns {*|boolean|boolean}
 */
const getUserOption = (key) => {
    let user = JSON.parse(localStorage.getItem('user'));
    if (!user) {
        return false;
    }
    return user.options[key] || false;
};

const getUserOptionAsync = async (key) => {
    try {

        if (typeof userOptionsCached[key] !== 'undefined' && userOptionsCached[key].ts > new Date().getTime() - 1000 * 30) {
            return userOptionsCached[key].value
        }
        else {

            let response = await Rest.authRequest('/user/self_options?name=' + key, 'GET');
            userOptionsCached[key] = {
                ts : new Date().getTime(),
                value : response.value
            };
            return response.value;
        }
    }
    catch(error) {
        return null;
    }
};


const resyncUserDatas = async () => {
    let token = localStorage.getItem('token');
    let datas = await testToken(token);
    localStorage.setItem('user', JSON.stringify(datas.user));

};

/**
 * @deprecated User vuex getter login.loggedUser
 * @returns {any}
 */
const getLoggedUser = () => {
    return JSON.parse(localStorage.getItem('user'));
};

const list = (options) => {

    options = options || {};
    return Rest.authRequest('/user?'+objectToUrl(options),'GET');
};

const getTempToken = () => {
    return Rest.authRequest('/user/temptoken','GET');
};

const getMyNotifications = () => {
    return Rest.authRequest('/user/mynotifications','GET');
};

const add = (user) => {
    return Rest.authRequest('/user','POST',user);
};

const deleteItem = (id) => {
    return Rest.authRequest('/user/'+id,'DELETE');
};


const selectList = () => {
    return new Promise((resolve,reject) => {
        list().then((users) => {
            let output = {};
            users['users'].forEach((user) => {
                output[user.id] = user.nickname || user.email;
            });

            resolve(output);
        },reject);
    });
};

const updateUserOption = (key,value) => {
    let user = JSON.parse(localStorage.getItem('user'));
    return Rest.authRequest('/user/selfoptions','POST',{
        id_user : user.id,
        key : key,
        value : value,
    }).then(() => {
        user.options[key] = value;
        localStorage.setItem('user' , JSON.stringify(user));
    })
};


export {
    testToken,
    hasPermission,
    list,
    add,
    deleteItem,
    selectList,
    updateUserOption,
    getUserOption,
    getLoggedUser,
    getMyNotifications,
    getTempToken,
    resyncUserDatas,
    getUserOptionAsync
}
