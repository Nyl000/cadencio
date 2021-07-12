import Rest from 'js/Services/Rest';
import {testToken} from 'js/Models/User';


const state = {
    token: '',
    loggedUser : {},
};

if (localStorage.getItem('token')) {
    state.token = localStorage.getItem('token');
}
if (localStorage.getItem('user')) {
    state.loggedUser = JSON.parse(localStorage.getItem('user'));
}


const getters = {

    token: (state) => state.token,
    loggedUser : (state) => state.loggedUser

};

const mutations = {
    setToken(state, token) {
        state.token = token;
    },
    setLoggedUser(state,userObject) {
        state.loggedUser = userObject;
    }
};

const actions = {
    loginAsync({commit, state}, datas) {

        let {username,password} = datas;

        return new Promise(async (resolve, reject) => {
            try {
                let loginResult = await Rest.request('/user/login', 'POST', {
                    email: username,
                    password: password,
                    use_jwt: true,
                });

                if (loginResult.status === 'ok') {
                    localStorage.setItem('token', loginResult.token);
                    commit('setToken', loginResult.token);
                    resolve()
                } else {
                    reject('Wrong login/password');
                }
            } catch (error) {
                reject(error.message);
            }
        })
    },

    refreshUserInfosAsync({commit,state}) {
        return new Promise(async (resolve,reject) => {
            try {
                let datas = await testToken(state.token);
                localStorage.setItem('user', JSON.stringify(datas.user));

                commit('setLoggedUser',datas.user);
                resolve(datas);
            }
            catch(error) {
                reject(error);
            }
        });

    }

};

export default {
    namespaced: true,
    state,
    getters,
    actions,
    mutations
}
