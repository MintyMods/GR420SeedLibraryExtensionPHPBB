import { DataEvents, DragEvents, IDataEventsHandlersMap, IDragEventsHandlersMap, IDataItem } from "../../ts-data";
import { GridEvents, IAdjustBy, IEventHandlersMap, IGrid, IGridConfig } from "../../ts-grid";
import { IEventSystem } from "../../ts-common/events";
export interface ITreeGridConfig extends IGridConfig {
    rootParent?: string;
}
export interface ITreeGrid extends IGrid {
    events: IEventSystem<DataEvents | GridEvents | DragEvents | TreeGridEvents, IEventHandlersMap & IDataEventsHandlersMap & IDragEventsHandlersMap & ITreeEventHandlersMap>;
    scrollTo(row: string, col: string): void;
    expand(id: string): void;
    collapse(id: string): void;
    expandAll(): void;
    collapseAll(): void;
    adjustColumnWidth(id: string | number, adjust?: IAdjustBy): void;
    groupBy(property: string | ((item: IDataItem) => string)): void;
    ungroup(): void;
}
export declare enum TreeGridEvents {
    beforeCollapse = "beforeCollapse",
    afterCollapse = "afterCollapse",
    beforeExpand = "beforeExpand",
    afterExpand = "afterExpand"
}
export interface ITreeEventHandlersMap {
    [key: string]: (...args: any[]) => any;
    [TreeGridEvents.beforeCollapse]: (id: string) => boolean | void;
    [TreeGridEvents.afterCollapse]: (id: string) => any;
    [TreeGridEvents.beforeExpand]: (id: string) => boolean | void;
    [TreeGridEvents.afterExpand]: (id: string) => any;
}
