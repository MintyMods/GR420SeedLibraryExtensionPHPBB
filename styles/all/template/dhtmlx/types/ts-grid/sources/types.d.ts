import { IEventSystem } from "../../ts-common/events";
import { IKeyManager } from "../../ts-common/KeyManager";
import { IAlign } from "../../ts-common/html";
import { IDataCollection, IDragConfig, ICsvDriverConfig, IDataItem, IDragInfo } from "../../ts-data";
import { Exporter } from "./Exporter";
import { IComboFilterConfig } from "../../ts-combobox";
import { IHandlers } from "../../ts-common/types";
export interface IGridConfig extends IDragConfig {
    columns?: ICol[];
    spans?: ISpan[];
    data?: any[];
    type?: "tree";
    width?: number;
    height?: number;
    sortable?: boolean;
    rowCss?: (row: IRow) => string;
    leftSplit?: number;
    selection?: ISelectionType;
    multiselection?: boolean;
    dragItem?: IDragType;
    keyNavigation?: boolean;
    hotkeys?: IHandlers;
    css?: string;
    editable?: boolean;
    autoEmptyRow?: boolean;
    resizable?: boolean;
    htmlEnable?: boolean;
    tooltip?: boolean;
    headerRowHeight?: number;
    footerRowHeight?: number;
    rowHeight?: number;
    adjust?: IAdjustBy;
    autoWidth?: boolean;
    autoHeight?: boolean;
    eventHandlers?: {
        [key: string]: any;
    };
    rootParent?: string;
    $headerLevel?: number;
    $footerLevel?: number;
    $totalWidth?: number;
    $totalHeight?: number;
    $positions?: IPositions;
    $colspans?: boolean;
    $footer?: boolean;
    $editable?: {
        row: any;
        col: any;
        editorType?: EditorType;
        editor?: IEditor;
    };
    $resizing?: string | number;
    groupTitleTemplate?: (groupName: string, groupItems: IDataItem[]) => string;
    /** @deprecated See a documentation: https://docs.dhtmlx.com/ */
    editing?: boolean;
    /** @deprecated See a documentation: https://docs.dhtmlx.com/ */
    headerSort?: boolean;
    /** @deprecated See a documentation: https://docs.dhtmlx.com/ */
    columnsAutoWidth?: boolean;
    /** @deprecated See a documentation: https://docs.dhtmlx.com/ */
    fitToContainer?: boolean;
    /** @deprecated See a documentation: https://docs.dhtmlx.com/ */
    splitAt?: number;
}
export interface IColumnsWidth {
    [col: string]: number;
}
export interface IScrollState {
    left: number;
    top: number;
}
export interface IRendererConfig extends IGridConfig {
    scroll?: IScrollState;
    datacollection: any;
    currentColumns?: ICol[];
    currentRows?: IRow[];
    firstColId?: string | number;
    headerHeight?: number;
    footerHeight?: number;
    events?: IEventSystem<GridEvents, IEventHandlersMap>;
    fixedColumnsWidth?: number;
    selection: any;
    sortBy?: string | number;
    sortDir?: string;
    filterLocation?: string;
    htmlEnable?: boolean;
    content?: IContentList;
    gridId?: string;
    _events?: IEventSystem<GridSystemEvents>;
}
export interface IGrid {
    data: IDataCollection;
    export: Exporter;
    config: IGridConfig;
    events: IEventSystem<GridEvents, IEventHandlersMap>;
    selection: ISelection;
    content: IContentList;
    keyManager: IKeyManager;
    paint(): void;
    destructor(): void;
    setColumns(col: ICol[]): void;
    addRowCss(id: any, css: string): void;
    removeRowCss(id: any, css: string): void;
    addCellCss(row: string, col: string, css: string): void;
    removeCellCss(row: string, col: string, css: string): void;
    getRootView(): any;
    showColumn(colId: string | number): void;
    hideColumn(colId: string | number): void;
    isColumnHidden(colId: string | number): boolean;
    showRow(rowId: string | number): void;
    hideRow(rowId: string | number): void;
    isRowHidden(rowId: string | number): boolean;
    scroll(x?: number, y?: number): void;
    scrollTo(row: string, col: string): void;
    getScrollState(): ICoords;
    adjustColumnWidth(id: string | number, adjust?: IAdjustBy): void;
    getCellRect(row: string | number, col: string | number): ICellRect;
    getColumn(colId: string): ICol;
    addSpan(spanObj: ISpan): void;
    getSpan(row: string | number, col: string | number): ISpan;
    removeSpan(row: string | number, col: string | number): void;
    editCell(rowId: string | number, colId: string | number, editorType?: EditorType): void;
    editEnd(withoutSave?: boolean): void;
    getSortingState(): any;
    getHeaderFilter(colId: string | number): any;
    /** @deprecated See a documentation: https://docs.dhtmlx.com/ */
    edit(rowId: string | number, colId: string | number, editorType?: EditorType): void;
}
export declare type EditorType = "input" | "select" | "datePicker" | "checkbox" | "combobox" | "textarea";
export interface ICellRect extends ICoords, ISizes {
}
export declare type colType = "string" | "number" | "boolean" | "date" | "percent" | any;
export interface ICol {
    id: string | number;
    width?: number;
    header?: IHeader[];
    footer?: IFooter[];
    minWidth?: number;
    maxWidth?: number;
    mark?: IMark | MarkFunction;
    type?: colType;
    editorType?: EditorType;
    editable?: boolean;
    resizable?: boolean;
    sortable?: boolean;
    options?: any[];
    draggable?: boolean;
    format?: string;
    htmlEnable?: boolean;
    template?: (cellValue: any, row: IRow, col: ICol) => string;
    hidden?: boolean;
    adjust?: IAdjustBy;
    autoWidth?: boolean;
    align?: IAlign;
    tooltip?: boolean;
    tooltipTemplate?: (cellValue: any, row: IRow, col: ICol) => string;
    gravity?: number;
    $cellCss?: {
        [key: string]: string;
    };
    $uniqueData?: any[];
    $width?: number;
    $fixed?: boolean;
    dateFormat?: string;
    /** @deprecated See a documentation: https://docs.dhtmlx.com/ */
    editing?: boolean;
    /** @deprecated See a documentation: https://docs.dhtmlx.com/ */
    headerSort?: boolean;
    /** @deprecated See a documentation: https://docs.dhtmlx.com/ */
    columnsAutoWidth?: boolean;
    /** @deprecated See a documentation: https://docs.dhtmlx.com/ */
    fitToContainer?: boolean;
}
export declare type fixedRowContent = "inputFilter" | "selectFilter" | "comboFilter";
export declare type footerMethods = "avg" | "sum" | "max" | "min";
export interface IHeader {
    text?: string;
    colspan?: number;
    rowspan?: number;
    css?: any;
    content?: fixedRowContent | footerMethods;
    filterConfig?: IComboFilterConfig;
    align?: IAlign;
}
export interface IFooter {
    text?: string | number;
    css?: any;
    content?: fixedRowContent | footerMethods;
}
export interface ISpan {
    row: string | number;
    column: string | number;
    rowspan?: number;
    colspan?: number;
    text?: string | number;
    css?: string;
    tooltip?: boolean;
    tooltipTemplate?: (spanValue: any, span: ISpan) => string;
}
declare type MarkFunction = (cell: any, columnCells: any[], row: IRow, column: ICol) => string;
export interface IMark {
    min?: string;
    max?: string;
}
export interface IPositions {
    xStart: number;
    xEnd: number;
    yStart: number;
    yEnd: number;
}
export interface ICellCss {
    color: string;
    background: string;
    fontSize: number;
}
export interface IExportData {
    columns: Array<{
        width: number;
    }>;
    header: string[][];
    data: any[];
    styles: {
        cells: any[];
        css: {
            [key: string]: ICellCss;
        };
    };
}
export declare enum GridEvents {
    scroll = "scroll",
    expand = "expand",
    filterChange = "filterChange",
    beforeResizeStart = "beforeResizeStart",
    resize = "resize",
    afterResizeEnd = "afterResizeEnd",
    cellClick = "cellClick",
    cellRightClick = "cellRightClick",
    cellMouseOver = "cellMouseOver",
    cellMouseDown = "cellMouseDown",
    cellDblClick = "cellDblClick",
    headerCellClick = "headerCellClick",
    footerCellClick = "footerCellClick",
    headerCellMouseOver = "headerCellMouseOver",
    footerCellMouseOver = "footerCellMouseOver",
    headerCellMouseDown = "headerCellMouseDown",
    footerCellMouseDown = "footerCellMouseDown",
    headerCellDblClick = "headerCellDblClick",
    footerCellDblClick = "footerCellDblClick",
    headerCellRightClick = "headerCellRightClick",
    footerCellRightClick = "footerCellRightClick",
    beforeEditStart = "beforeEditStart",
    afterEditStart = "afterEditStart",
    beforeEditEnd = "beforeEditEnd",
    afterEditEnd = "afterEditEnd",
    beforeKeyDown = "beforeKeyDown",
    afterKeyDown = "afterKeyDown",
    beforeColumnHide = "beforeColumnHide",
    afterColumnHide = "afterColumnHide",
    beforeColumnShow = "beforeColumnShow",
    afterColumnShow = "afterColumnShow",
    beforeRowHide = "beforeRowHide",
    afterRowHide = "afterRowHide",
    beforeRowShow = "beforeRowShow",
    afterRowShow = "afterRowShow",
    beforeRowDrag = "beforeRowDrag",
    dragRowStart = "dragRowStart",
    dragRowOut = "dragRowOut",
    dragRowIn = "dragRowIn",
    canRowDrop = "canRowDrop",
    cancelRowDrop = "cancelRowDrop",
    beforeRowDrop = "beforeRowDrop",
    afterRowDrop = "afterRowDrop",
    afterRowDrag = "afterRowDrag",
    beforeColumnDrag = "beforeColumnDrag",
    dragColumnStart = "dragColumnStart",
    dragColumnOut = "dragColumnOut",
    dragColumnIn = "dragColumnIn",
    canColumnDrop = "canColumnDrop",
    cancelColumnDrop = "cancelColumnDrop",
    beforeColumnDrop = "beforeColumnDrop",
    afterColumnDrop = "afterColumnDrop",
    afterColumnDrag = "afterColumnDrag",
    beforeRowResize = "beforeRowResize",
    afterRowResize = "afterRowResize",
    beforeSort = "beforeSort",
    afterSort = "afterSort",
    /** @deprecated See a documentation: https://docs.dhtmlx.com/ */
    headerInput = "headerInput"
}
export interface IEventHandlersMap {
    [key: string]: (...args: any[]) => any;
    [GridEvents.scroll]: (scrollState: ICoords) => void;
    [GridEvents.beforeSort]: (col: ICol, dir: Dirs) => void | boolean;
    [GridEvents.afterSort]: (col: ICol, dir: Dirs) => void;
    [GridEvents.expand]: (id: string) => void;
    [GridEvents.filterChange]: (value: string, colId: string, filterId: fixedRowContent) => void;
    [GridEvents.beforeResizeStart]: (col: ICol, e: MouseEvent) => boolean | void;
    [GridEvents.resize]: (col: ICol, e: MouseEvent) => void;
    [GridEvents.afterResizeEnd]: (col: ICol, e: MouseEvent) => void;
    [GridEvents.cellClick]: (row: IRow, col: ICol, e: MouseEvent) => void;
    [GridEvents.cellRightClick]: (row: IRow, col: ICol, e: MouseEvent) => void;
    [GridEvents.cellMouseOver]: (row: IRow, col: ICol, e: MouseEvent) => void;
    [GridEvents.cellMouseDown]: (row: IRow, col: ICol, e: MouseEvent & TouchEvent) => void;
    [GridEvents.cellDblClick]: (row: IRow, col: ICol, e: MouseEvent) => void;
    [GridEvents.headerCellClick]: (col: ICol, e: MouseEvent) => void;
    [GridEvents.footerCellClick]: (col: ICol, e: MouseEvent) => void;
    [GridEvents.headerCellMouseOver]: (col: ICol, e: MouseEvent) => void;
    [GridEvents.footerCellMouseOver]: (col: ICol, e: MouseEvent) => void;
    [GridEvents.headerCellMouseDown]: (col: ICol, e: MouseEvent & TouchEvent) => void;
    [GridEvents.footerCellMouseDown]: (col: ICol, e: MouseEvent & TouchEvent) => void;
    [GridEvents.headerCellDblClick]: (col: ICol, e: MouseEvent) => void;
    [GridEvents.footerCellDblClick]: (col: ICol, e: MouseEvent) => void;
    [GridEvents.headerCellRightClick]: (col: ICol, e: MouseEvent) => void;
    [GridEvents.footerCellRightClick]: (col: ICol, e: MouseEvent) => void;
    [GridEvents.beforeEditStart]: (row: IRow, col: ICol, editorType: EditorType) => boolean | void;
    [GridEvents.afterEditStart]: (row: IRow, col: ICol, editorType: EditorType) => void;
    [GridEvents.beforeEditEnd]: (value: any, row: IRow, col: ICol) => boolean | void;
    [GridEvents.afterEditEnd]: (value: any, row: IRow, col: ICol) => void;
    [GridEvents.beforeKeyDown]: (e: Event) => boolean | void;
    [GridEvents.afterKeyDown]: (e: Event) => void;
    [GridEvents.beforeColumnHide]: (col: ICol) => boolean | void;
    [GridEvents.afterColumnHide]: (col: ICol) => void;
    [GridEvents.beforeColumnShow]: (col: ICol) => boolean | void;
    [GridEvents.afterColumnShow]: (col: ICol) => void;
    [GridEvents.beforeRowHide]: (row: IRow) => boolean | void;
    [GridEvents.afterRowHide]: (row: IRow) => void;
    [GridEvents.beforeRowShow]: (row: IRow) => boolean | void;
    [GridEvents.afterRowShow]: (row: IRow) => void;
    [GridEvents.beforeRowDrag]: (data: IDragInfo, events: MouseEvent) => void | boolean;
    [GridEvents.dragRowStart]: (data: IDragInfo, events: MouseEvent) => any;
    [GridEvents.dragRowOut]: (data: IDragInfo, events: MouseEvent) => any;
    [GridEvents.dragRowIn]: (data: IDragInfo, events: MouseEvent) => void | boolean;
    [GridEvents.canRowDrop]: (data: IDragInfo, events: MouseEvent) => any;
    [GridEvents.cancelRowDrop]: (data: IDragInfo, events: MouseEvent) => any;
    [GridEvents.beforeRowDrop]: (data: IDragInfo, events: MouseEvent) => void | boolean;
    [GridEvents.afterRowDrop]: (data: IDragInfo, events: MouseEvent) => any;
    [GridEvents.afterRowDrag]: (data: IDragInfo, events: MouseEvent) => any;
    [GridEvents.beforeColumnDrag]: (data: IDragInfo, events: MouseEvent) => void | boolean;
    [GridEvents.dragColumnStart]: (data: IDragInfo, events: MouseEvent) => any;
    [GridEvents.dragColumnOut]: (data: IDragInfo, events: MouseEvent) => any;
    [GridEvents.dragColumnIn]: (data: IDragInfo, events: MouseEvent) => void | boolean;
    [GridEvents.canColumnDrop]: (data: IDragInfo, events: MouseEvent) => any;
    [GridEvents.cancelColumnDrop]: (data: IDragInfo, events: MouseEvent) => any;
    [GridEvents.beforeColumnDrop]: (data: IDragInfo, events: MouseEvent) => void | boolean;
    [GridEvents.afterColumnDrop]: (data: IDragInfo, events: MouseEvent) => any;
    [GridEvents.afterColumnDrag]: (data: IDragInfo, events: MouseEvent) => any;
    [GridEvents.beforeRowResize]: (row: IRow, events: Event, currentHeight: number) => boolean;
    [GridEvents.afterRowResize]: (row: IRow, events: Event, currentHeight: number) => void;
    [GridEvents.headerInput]: (value: string, colId: string, filterId: fixedRowContent) => void;
}
export declare enum GridSystemEvents {
    cellTouchMove = "cellTouchMove",
    cellTouchEnd = "cellTouchEnd",
    headerCellTouchMove = "headerCellTouchMove",
    headerCellTouchEnd = "headerCellTouchEnd"
}
export interface ISystemEventHandlersMap {
    [key: string]: (...args: any[]) => any;
    [GridSystemEvents.cellTouchMove]: (row: IRow, col: ICol, e: TouchEvent) => void;
    [GridSystemEvents.cellTouchEnd]: (row: IRow, col: ICol, e: TouchEvent) => void;
    [GridSystemEvents.headerCellTouchMove]: (col: ICol, e: TouchEvent) => void;
    [GridSystemEvents.headerCellTouchEnd]: (col: ICol, e: TouchEvent) => void;
}
export interface ICellContent {
    element?: any;
    toHtml: (column: ICol, config: IRendererConfig) => any;
    match?: (obj: any, value: any) => boolean;
    destroy?: () => void;
    calculate?: (col: any[], roots: any[]) => string | number;
    validate?: (colId: string, data: any[]) => any[];
    value?: any;
}
export interface IContentList {
    [key: string]: ICellContent;
}
export interface ILayoutState {
    wrapper: ISizes;
    shifts: ICoords;
    sticky: boolean;
    gridBodyHeight: number;
}
export interface IFixedRowsConfig extends ILayoutState {
    name: "header" | "footer";
    position: "top" | "bottom";
}
export interface IXlsxExportConfig {
    url?: string;
    name?: string;
}
export interface ICsvExportConfig extends ICsvDriverConfig {
    name?: string;
    asFile?: boolean;
    flat?: boolean;
    rowDelimiter?: string;
    columnDelimiter?: string;
}
export declare type Dirs = "asc" | "desc";
export interface ICoords {
    x: number;
    y: number;
}
export interface ISizes {
    width: number;
    height: number;
}
export interface ICell {
    row: IRow;
    column: ICol;
}
export interface IRow {
    id?: string | number;
    height?: number;
    $height?: number;
    [key: string]: any;
}
export interface IEditor {
    toHTML(): any;
    endEdit(withoutSave?: boolean): void;
}
export declare type ISelectionType = "cell" | "row" | "complex";
export declare type IDirection = "horizontal" | "vertical";
export declare type IDragType = "row" | "column" | "complex";
export declare type IAdjustBy = "data" | "header" | "footer" | boolean;
export interface ISelectionConfig {
    disabled?: boolean;
}
export interface ISelection {
    config?: ISelectionConfig;
    setCell(row?: any, col?: any, ctrlUp?: boolean, shiftUp?: boolean): void;
    getCell(): ICell;
    getCells(): ICell[];
    removeCell(rowId?: string | number, colId?: string | number): void;
    disable(): void;
    enable(): void;
    toHTML(): any | any[];
}
export declare enum GridSelectionEvents {
    beforeUnSelect = "beforeUnSelect",
    afterUnSelect = "afterUnSelect",
    beforeSelect = "beforeSelect",
    afterSelect = "afterSelect"
}
export interface IGridSelectionEventsHandlersMap {
    [key: string]: (...args: any[]) => any;
    [GridSelectionEvents.afterSelect]: (row: IRow, col: ICol) => void;
    [GridSelectionEvents.afterUnSelect]: (row: IRow, col: ICol) => void;
    [GridSelectionEvents.beforeSelect]: (row: IRow, col: ICol) => boolean | void;
    [GridSelectionEvents.beforeUnSelect]: (row: IRow, col: ICol) => boolean | void;
}
export {};
