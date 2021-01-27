import { VNode } from "../../ts-common/dom";
import { List } from "../../ts-list";
import { IHandlers } from "../../ts-common/types";
import { IDataViewConfig, IDataView } from "./types";
export declare class DataView extends List implements IDataView {
    config: IDataViewConfig;
    constructor(node: HTMLElement | string, config?: IDataViewConfig);
    showItem(id: string): void;
    protected _didRedraw(vm: any): void;
    protected _renderItem(item: any, index: number): VNode;
    protected _renderList(): VNode;
    protected _getHotkeys(): IHandlers;
}
