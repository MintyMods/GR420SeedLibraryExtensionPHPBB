import { GridEvents, IAdjustBy, IEventHandlersMap, ProGrid, ICellRect, IRow, ICol, IColumnsWidth } from "../../ts-grid";
import { IEventSystem } from "../../ts-common/events";
import { DataEvents, DragEvents, IDataEventsHandlersMap, IDragEventsHandlersMap, IDataItem } from "../../ts-data";
import { TreeGridCollection } from "./TreeGridCollection";
import { ITreeEventHandlersMap, ITreeGrid, ITreeGridConfig, TreeGridEvents } from "./types";
export declare class TreeGrid extends ProGrid implements ITreeGrid {
    data: TreeGridCollection;
    events: IEventSystem<DataEvents | GridEvents | DragEvents | TreeGridEvents, IEventHandlersMap & IDataEventsHandlersMap & IDragEventsHandlersMap & ITreeEventHandlersMap>;
    private _pregroupData;
    constructor(container: HTMLElement, config: ITreeGridConfig);
    scrollTo(row: string, col: string): void;
    expand(id: string): void;
    collapse(id: string): void;
    expandAll(): void;
    collapseAll(): void;
    groupBy(property: string | ((item: IDataItem) => string)): void;
    ungroup(): void;
    showRow(id: string): void;
    hideRow(id: string): void;
    getCellRect(row: string | number, col: string | number): ICellRect;
    protected _adjustColumnsWidth(rows: IRow[], cols: ICol[], adjust?: IAdjustBy): IColumnsWidth;
    protected _createCollection(prep: (data: any[]) => any[]): void;
    protected _getRowIndex(rowId: any): number;
    protected _setEventHandlers(): void;
    private _groupBy;
}
