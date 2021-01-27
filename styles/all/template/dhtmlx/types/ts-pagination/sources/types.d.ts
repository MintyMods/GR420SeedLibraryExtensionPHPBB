import { DataCollection } from "../../ts-data";
export interface IPagination {
    data: DataCollection;
    config: IPaginationConfig;
    destructor(): void;
    setPage(v: number): void;
    getPage(): number;
    setPageSize(v: number): void;
    getPageSize(): number;
    getPagesCount(): number;
}
export interface IPaginationConfig {
    data: DataCollection;
    css?: string;
    page?: number;
    pageSize?: number;
    inputWidth?: number;
}
export declare enum PaginationEvents {
    change = "change"
}
