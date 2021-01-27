import { IEventSystem } from "../../ts-common/events";
import { View } from "../../ts-common/view";
import { DataEvents, DragEvents, IDataCollection, IDataEventsHandlersMap, IDragEventsHandlersMap } from "../../ts-data";
import { Exporter } from "./Exporter";
import { Dirs, EditorType, GridEvents, IAdjustBy, ICellRect, ICol, IContentList, ICoords, IEventHandlersMap, IGrid, IGridConfig, IScrollState, ISelection, ISpan, GridSystemEvents, ISystemEventHandlersMap, IKeyManager } from "./types";
import { ITouchParam } from "../../ts-common/types";
export declare class Grid extends View implements IGrid {
    data: IDataCollection;
    config: IGridConfig;
    events: IEventSystem<DataEvents | GridEvents | DragEvents, IEventHandlersMap & IDataEventsHandlersMap & IDragEventsHandlersMap>;
    export: Exporter;
    content: IContentList;
    selection: ISelection;
    keyManager: IKeyManager;
    protected _touch: ITouchParam;
    protected _scroll: IScrollState;
    protected _events: IEventSystem<GridSystemEvents, ISystemEventHandlersMap>;
    private _sortDir;
    private _sortBy;
    private _filterData;
    private _activeFilters;
    constructor(container: HTMLElement | string, config?: IGridConfig);
    destructor(): void;
    setColumns(columns: ICol[]): void;
    addRowCss(id: string, css: string): void;
    removeRowCss(id: string, css: string): void;
    addCellCss(row: string, col: string | number, css: string): void;
    removeCellCss(row: string, col: string | number, css: string): void;
    showColumn(colId: string | number): void;
    hideColumn(colId: string | number): void;
    isColumnHidden(colId: string | number): boolean;
    showRow(rowId: string | number): void;
    hideRow(rowId: string | number): void;
    isRowHidden(rowId: string | number): boolean;
    getScrollState(): ICoords;
    scroll(x: number, y: number): void;
    scrollTo(row: string, col: string): void;
    adjustColumnWidth(id: string | number, adjust?: IAdjustBy): void;
    getCellRect(row: string | number, col: string | number): ICellRect;
    getColumn(colId: string): ICol;
    addSpan(spanObj: ISpan): void;
    getSpan(row: string | number, col: string | number): ISpan;
    removeSpan(row: string | number, col: string | number): void;
    editCell(rowId: string | number, colId: string | number, editorType?: EditorType): void;
    editEnd(withoutSave?: boolean): void;
    getSortingState(): {
        dir: import("../../ts-grid").Dirs;
        by: string;
    };
    getHeaderFilter(colId: string | number): any;
    /** @deprecated See a documentation: https://docs.dhtmlx.com/ */
    edit(rowId: string | number, colId: string | number, editorType?: EditorType): void;
    protected _parseColumns(): void;
    protected _parseData(): void;
    protected _checkColumns(): void;
    protected _createCollection(prep: (data: any[]) => any[]): void;
    protected _getRowIndex(rowId: any): number;
    protected _setEventHandlers(): void;
    protected _addEmptyRow(): void;
    protected _sort(by: string, dir?: Dirs): void;
    protected _clearTouchTimer(): void;
    private _dragStart;
    private _getColumn;
    private _init;
    private _attachDataCollection;
    private _setMarks;
    private _checkMarks;
    private _removeMarks;
    private _adjustColumns;
    private _detectColsTypes;
    private _checkFilters;
    private _destroyContent;
    private _render;
    private _lazyLoad;
}
