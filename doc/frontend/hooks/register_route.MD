
#register_menu_section

Register a route into the app url router

##Properties

- path : The route path to register
- component : the Vue component to register for the route

##Example
````javascript
import {addHook} from 'js/Services/HookHandler';

addHook('register_route', () => {
    return {
        path: '/route/to/example', 
        component: ExampleComponent
    }
});
````