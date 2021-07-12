import Rest from 'js/Services/Rest';
import {testToken} from 'js/Models/User';


const state = {
    token: '',
    loggedUser : {},
    userOptionsCached : {},
};

if (localStorage.getItem('token')) {
    state.token = localStorage.getItem('token');
}
if (localStorage.getItem('user')) {
    state.loggedUser = JSON.parse(localStorage.getItem('user'));
}


const getters = {

    token: (state) => state.token,
    loggedUser : (state) => state.loggedUser,

};

const mutations = {
    setToken(state, token) {
        state.token = token;
    },
    setLoggedUser(state,userObject) {
        state.loggedUser = userObject;
    },
    updateUserOptionCached(state,payload) {
        let {key,val} = payload;
        state.userOptionsCached[key] = {
            ts: new Date().getTime(),
            value: val
        }
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
    },
    getUserOptionAsync ({commit,state}, option) {
        return new Promise(async (resolve,reject) => {
            if (typeof state.userOptionsCached[option] !== 'undefined' && state.userOptionsCached[option].ts > new Date().getTime() - 1000 * 30) {
                resolve(userOptionsCached[option].value)
            }
            else {
                try {
                    let response = await Rest.authRequest('/user/self_options?name=' + option, 'GET');
                    commit('updateUserOptionCached',{key :option, val :response.value});
                    resolve(response.value);
                }
                catch(error) {
                    resolve(null);
                }
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
