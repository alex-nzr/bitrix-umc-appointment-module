// language=Vue
import { createPinia, setActivePinia } from 'ui.vue3.pinia';
import {BitrixVue} from 'ui.vue3';
import {Wrapper} from "../components/Wrapper/Wrapper";
import {appStore} from '../stores/appStore'
import '../css/main.css';

export class Application
{
	#application: any = null;
	#store: any = null;

	constructor(root, options = {})
	{
		this.root = root;
		this.name = options.name;
		this.useCustomBtn = options.useCustomBtn;
		this.customBtnId = options.customBtnId;

		this.#store = createPinia();

		this.start();
	}

	start(): void
	{
		const context = this;

		setActivePinia(this.#store);
		appStore().setUseCustomBtn(context.useCustomBtn);

		this.#application = BitrixVue.createApp({
			name: 'Appointment application root',
			components: {Wrapper},
			computed: {},
			methods: {},
			mounted()
			{
				if (context.useCustomBtn && context.customBtnId)
				{
					BX(context.customBtnId)?.addEventListener('click', () => {
						//Todo toggle app form
						console.log('Custom btn click');
					});
				}
			},
			template: `<Wrapper/>`
		});
		this.#application.use(this.#store);
		this.#application.mount(this.root);
	}
}