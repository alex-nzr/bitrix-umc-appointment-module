import type {EntityModel} from "../EntityModel";

export class Entity implements EntityModel
{
    constructor(uid: string, name: string, duration: number = undefined, price: number = undefined)
    {
        this.uid = uid;
        this.name = name;
        this.duration = duration;
        this.price = price;
    }

    getUid(): string
    {
        return this.uid
    }

    getName(): string
    {
        return this.name
    }

    getDuration(): number
    {
        return this.hasOwnProperty('duration') ? this.duration : undefined;
    }

    getPrice(): number
    {
        return this.hasOwnProperty('price') ? this.price : undefined;
    }
}