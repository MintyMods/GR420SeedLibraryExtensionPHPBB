import { IDataItem, DataCollection, DataEvents, DragEvents, IDataEventsHandlersMap, IDragEventsHandlersMap, IDragConfig } from "../../ts-data";
import { IEventSystem } from "../../ts-common/events";
import { IHandlers, SelectionEvents, ISelectionEventsHandlersMap } from "../../ts-common/types";
import { IKeyManager } from "../../ts-common/KeyManager";
export declare type MultiselectionMode = "click" | "ctrlClick";
export interface IListConfig extends IDragConfig {
    template?: (obj: IDataItem) => string;
    data?: DataCollection<any> | any[];
    virtual?: boolean;
    itemHeight?: number | string;
    css?: string;
    height?: number | string;
    selection?: boolean;
    multiselection?: boolean | MultiselectionMode;
    keyNavigation?: boolean | (() => boolean);
    editable?: boolean;
    hotkeys?: IHandlers;
    eventHandlers?: {
        [key: string]: any;
    };
    /** @deprecated See a documentation: https://docs.dhtmlx.com/ */
    editing?: boolean;
    /** @deprecated See a documentation: https://docs.dhtmlx.com/ */
    multiselectionMode?: MultiselectionMode;
}
export declare enum ListEvents {
    click = "click",
    doubleClick = "doubleclick",
    focusChange = "focuschange",
    beforeEditStart = "beforeEditStart",
    afterEditStart = "afterEditStart",
    beforeEditEnd = "beforeEditEnd",
    afterEditEnd = "afterEditEnd",
    itemRightClick = "itemRightClick",
    itemMouseOver = "itemMouseOver",
    /** @deprecated See a documentation: https://docs.dhtmlx.com/ */
    contextmenu = "contextmenu"
}
export interface IListEventHandlersMap {
    [key: string]: (...args: any[]) => any;
    [ListEvents.click]: (id: string, e: Event) => any;
    [ListEvents.itemMouseOver]: (id: string, e: Event) => any;
    [ListEvents.doubleClick]: (id: string, e: Event) => any;
    [ListEvents.itemRightClick]: (id: string, e: MouseEvent) => any;
    [ListEvents.focusChange]: (focusIndex: number, id: string) => any;
    [ListEvents.beforeEditStart]: (id: string) => void | boolean;
    [ListEvents.afterEditStart]: (id: string) => void;
    [ListEvents.beforeEditEnd]: (value: any, id: string) => void | boolean;
    [ListEvents.afterEditEnd]: (value: any, id: string) => void;
    [ListEvents.contextmenu]: (id: string, e: MouseEvent) => any;
}
export interface ISelectionConfig {
    multiselection?: boolean | MultiselectionMode;
    disabled?: boolean;
}
export interface IList<T = any> {
    config: IListConfig;
    data: DataCollection<T>;
    events: IEventSystem<DataEvents | ListEvents | DragEvents, IListEventHandlersMap & IDataEventsHandlersMap & IDragEventsHandlersMap>;
    selection: ISelection;
    keyManager: IKeyManager;
    paint(): void;
    destructor(): void;
    editItem(id: string): void;
    getFocusItem(): T;
    setFocus(id: string): void;
    getFocus(): string;
    showItem(id: string): void;
    /** @deprecated See a documentation: https://docs.dhtmlx.com/ */
    disableSelection(): void;
    /** @deprecated See a documentation: https://docs.dhtmlx.com/ */
    enableSelection(): void;
}
export interface ISelection<T = any> {
    config: ISelectionConfig;
    events: IEventSystem<SelectionEvents | DataEvents, ISelectionEventsHandlersMap & IDataEventsHandlersMap>;
    getId(): string | string[] | undefined;
    getItem(): T;
    contains(id?: string): boolean;
    remove(id?: string): void;
    add(id?: string, isShift?: boolean, isCtrl?: boolean, silent?: boolean): void;
    enable(): void;
    disable(): void;
    destructor(): void;
}
export interface IListItem {
    [key: string]: any;
}
