const registeredHooks = {};

const addHook = (name, hookFunc) => {
    if (typeof(registeredHooks[name]) === 'undefined') {
        registeredHooks[name] = [];
    }
    registeredHooks[name].push(hookFunc);

};

const getHooks = (hookName) => {
    return (typeof(registeredHooks[hookName]) === 'undefined') ? [] : registeredHooks[hookName];
};


export  {
    addHook,
    getHooks

}