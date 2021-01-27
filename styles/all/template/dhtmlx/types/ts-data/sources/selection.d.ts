import { IEventSystem } from "../../ts-common/events";
import { SelectionEvents, ISelection, ISelectionConfig } from "../../ts-common/types";
import { DataCollection } from "./datacollection";
export declare class Selection implements ISelection {
    events: IEventSystem<SelectionEvents>;
    config: ISelectionConfig;
    private _selected;
    private _data;
    constructor(config: ISelectionConfig, data?: DataCollection, events?: IEventSystem<any>);
    getId(): string;
    getItem(): any;
    remove(id?: string): boolean;
    add(id: string): void;
    enable(): void;
    disable(): void;
    private _addSingle;
}
