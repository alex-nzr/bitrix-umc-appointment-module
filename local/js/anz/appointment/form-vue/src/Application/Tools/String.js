export class String
{
    ucFirst(str: string): string
    {
        if (!str) return str;

        return str[0].toUpperCase() + str.slice(1);
    }

    uniqId(prefix = '') {
        const date = new Date();
        const uniqueTimestamp = date.getTime().toString(16);
        const randomNum = Math.floor(Math.random() * 10000).toString(16);
        return prefix + uniqueTimestamp + randomNum;
    }
}