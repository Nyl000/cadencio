
#register_route

Register a menu section into the main menu

##Properties

- name : Unique name used to register menu entries
- title : Title showed
- canDisplay : Manage when the section can be showed or not.


##Example
````javascript
import {addHook} from 'js/Services/HookHandler';

addHook('register_menu_section', () => {
    return {
        name: 'menu_section_example',
        title: 'Example section',
        canDisplay: hasPermission('*', '*')
    }
});

````