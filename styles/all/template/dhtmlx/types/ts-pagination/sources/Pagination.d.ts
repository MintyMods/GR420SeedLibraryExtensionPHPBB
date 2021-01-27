import { IEventSystem } from "../../ts-common/events";
import { DataCollection } from "../../ts-data";
import { View } from "../../ts-common/view";
import { IPagination, IPaginationConfig, PaginationEvents } from "./types";
export declare class Pagination extends View implements IPagination {
    config: IPaginationConfig;
    events: IEventSystem<PaginationEvents>;
    data: DataCollection;
    private _toolbar;
    private _page;
    private _size;
    constructor(container: HTMLElement | string, config: IPaginationConfig);
    paint(): void;
    destructor(): void;
    setPage(page: number, necessarily?: boolean): void;
    getPage(): number;
    setPageSize(size: number): void;
    getPageSize(): number;
    getPagesCount(): number;
    private _showItem;
    private _showTreeItem;
    private _updateSizeUI;
    private _initUI;
    private _initEvents;
    private _render;
}
