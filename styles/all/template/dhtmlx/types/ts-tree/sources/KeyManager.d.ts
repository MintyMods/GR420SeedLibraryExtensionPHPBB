import { anyFunction } from "../../ts-common/types";
import { ITree } from "./types";
interface IKeyManager {
    addHotKey(key: string, handler: anyFunction): void;
    isFocus(): boolean;
}
export declare class KeyManager implements IKeyManager {
    protected _tree: ITree;
    protected _focusedId: string | number;
    constructor(tree: ITree);
    addHotKey(key: string, handler: anyFunction): void;
    isFocus(): boolean;
    protected _initFocusHandlers(): void;
    protected _initHotKeys(): void;
    private _getClosestBot;
    private _getClosestTop;
    private _getFocused;
}
export {};
