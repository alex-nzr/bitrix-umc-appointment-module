import type {Entity} from "../Model/Resource/Entity";

export class OneCState
{
    getters = {};
    actions = {};

    constructor() {
        this.getters = this.collectGetters();
        this.actions = this.collectActions();
    }

    getStoreName(){
        return 'oneCState';
    }

    state() {
        return {

        };
    }

    collectGetters()
    {
        return {

        };
    }

    collectActions()
    {
        return {

        };
    }
}