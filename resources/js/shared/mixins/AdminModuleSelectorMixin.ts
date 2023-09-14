import {useAdminDataStore} from "@js/services/state/states";

export default {

    data() {
        return {
            selected_module_id: null
        }
    }, methods: {
        onChangeAdminModule(module_id) {
            console.info(module_id);
        }
    }, mounted() {
        const store = useAdminDataStore();
        this.selected_module_id = store.module_id;
        store.$subscribe((mutate, state) => {
            if (this.selected_module_id !== state.module_id) {
                this.selected_module_id = state.module_id;
                this.onChangeAdminModule(state.module_id);
            }

        })
        console.info("Mounted");
    }
};

