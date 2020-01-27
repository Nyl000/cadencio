const registeredEvents = {};

const addEvent = (name, eventFunc) => {
    if (typeof(registeredEvents[name]) === 'undefined') {
        registeredEvents[name] = [];
    }
    registeredEvents[name].push(eventFunc);

};

const getEvents = (eventName) => {
    return (typeof(registeredEvents[eventName]) === 'undefined') ? [] : registeredEvents[eventName];
};


export  {
    addEvent,
    getEvents

}