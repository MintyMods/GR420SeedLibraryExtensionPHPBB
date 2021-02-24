import { DataCollection, DataEvents, DragEvents, IDataEventsHandlersMap, IDragEventsHandlersMap } from "../../ts-data";
import { VNode } from "../../ts-common/dom";
import { IEventSystem } from "../../ts-common/events";
import { IKeyManager } from "../../ts-common/KeyManager";
import { IHandlers } from "../../ts-common/types";
import { View } from "../../ts-common/view";
import { IList, IListConfig, IListEventHandlersMap, IListItem, ISelection, ListEvents } from "./types";
export declare const MOVE_UP = 1;
export declare const MOVE_DOWN = 2;
export declare class List extends View implements IList {
    config: IListConfig;
    data: DataCollection;
    events: IEventSystem<DataEvents | ListEvents | DragEvents, IListEventHandlersMap & IDataEventsHandlersMap & IDragEventsHandlersMap>;
    selection: ISelection;
    keyManager: IKeyManager;
    protected _handlers: IHandlers;
    protected _focus: string;
    protected _edited: string;
    protected _events: IHandlers;
    private _topOffset;
    private _visibleHeight;
    private _touch;
    constructor(node: HTMLElement | string, config?: IListConfig);
    protected _didRedraw(vm: any): void;
    private _dblClick;
    private _clearTouchTimer;
    private _dragStart;
    /** @deprecated See a documentation: https://docs.dhtmlx.com/ */
    disableSelection(): void;
    /** @deprecated See a documentation: https://docs.dhtmlx.com/ */
    enableSelection(): void;
    editItem(id: string): void;
    editEnd(value: any, id?: string): void;
    getFocusItem(): any;
    setFocus(id: string): void;
    getFocus(): string;
    destructor(): void;
    showItem(id: string): void;
    protected _renderItem(item: IListItem, index: number): VNode;
    protected _renderList(): VNode;
    moveFocus(mode: number, step?: number): void;
    private _getRange;
    protected _getHotkeys(): IHandlers;
    private _initHotKey;
    private getItemAriaAttrs;
    private getListAriaAttrs;
}
