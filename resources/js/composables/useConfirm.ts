import {
    reactive,
} from 'vue'

export type ConfirmTone =
    | 'primary'
    | 'success'
    | 'warning'
    | 'danger'

export interface ConfirmOptions {
    title: string
    description?: string

    confirmText?: string
    cancelText?: string

    tone?: ConfirmTone
}

interface ConfirmState {
    open: boolean

    title: string
    description: string

    confirmText: string
    cancelText: string

    tone: ConfirmTone
}

const state = reactive<ConfirmState>({
    open: false,

    title: '',
    description: '',

    confirmText:
        'Konfirmasi',

    cancelText:
        'Batal',

    tone:
        'primary',
})

let resolver:
    ((result: boolean) => void)
    | null = null

/*
|--------------------------------------------------------------------------
| Ask Confirmation
|--------------------------------------------------------------------------
*/

const askConfirm = (
    options: ConfirmOptions,
): Promise<boolean> => {
    /*
     * Jika masih ada dialog sebelumnya,
     * tutup terlebih dahulu.
     */
    if (
        state.open &&
        resolver
    ) {
        resolver(false)

        resolver = null
    }

    state.title =
        options.title

    state.description =
        options.description ?? ''

    state.confirmText =
        options.confirmText ??
        'Konfirmasi'

    state.cancelText =
        options.cancelText ??
        'Batal'

    state.tone =
        options.tone ??
        'primary'

    state.open = true

    return new Promise<boolean>(
        (resolve) => {
            resolver = resolve
        },
    )
}

/*
|--------------------------------------------------------------------------
| Confirm
|--------------------------------------------------------------------------
*/

const confirm = () => {
    if (!state.open) {
        return
    }

    state.open = false

    resolver?.(true)

    resolver = null
}

/*
|--------------------------------------------------------------------------
| Cancel
|--------------------------------------------------------------------------
*/

const cancel = () => {
    if (!state.open) {
        return
    }

    state.open = false

    resolver?.(false)

    resolver = null
}

/*
|--------------------------------------------------------------------------
| Composable
|--------------------------------------------------------------------------
*/

export const useConfirm = () => {
    return {
        state,

        askConfirm,

        confirm,

        cancel,
    }
}