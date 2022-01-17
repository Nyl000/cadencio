
#register_menu

Register a menu entry into the main menu
A menu entry need a "section" registered by [register_menu_section hook](register_menu_section.MD).

##Properties

- icon : a VueJS component for the icon.
- section : The unique identifier of a registered menu section.
- canDisplay : Manage when the entry can be showed or not.
- to : The route handled by the menu entry


##Example
````javascript
import {addHook} from 'js/Services/HookHandler';

addHook('register_menu', () => {
    return {
        icon: UserIconComponent,
        section: 'section_example',
        title: 'Example Menu Entry',
        to: '/route/to/example',
        canDisplay: hasPermission('*', '*')
    }
});

````