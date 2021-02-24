import { PointType } from "../types";
export declare function hoverMode(cfg: any): any;
export declare function standarMode(cfg: any): any;
export declare function getHelper(type: PointType): any;
export declare function getHTMLHelper(type: PointType): any;
declare type drawFn = (x: number, y: number, id: string) => any;
export declare function getShadeHelper(type: PointType, color: string, mode: any): drawFn;
export declare function getShadeHTMLHelper(type: PointType, color: string): drawFn;
export {};
