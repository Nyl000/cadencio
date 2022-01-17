
#register_vuex_module

Register a vuex module

##Properties

- path : The route path to register
- component : the Vue component to register for the route

##Example

**ExampleVueStore.js**
```javascript

const state = {
    //...
};

const getters = {
    //...
};

const mutations = {
    //...
};

const actions = {
    //...
};

export default {
    namespaced: true,
    state,
    getters,
    actions,
    mutations
}

```

```javascript
import {addHook} from 'js/Services/HookHandler';

addHook('register_vuex_module', {
        ExampleVuexStore
 });
```