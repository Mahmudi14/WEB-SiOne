import {
    reactive,
} from 'vue'

export type ToastTone =
    | 'success'
    | 'error'
    | 'warning'
    | 'info'

export interface ToastItem {
    id: number
    message: string
    tone: ToastTone
    duration: number
}

const state = reactive<{
    items: ToastItem[]
}>({
    items: [],
})

let counter = 0

const removeToast = (
    id: number,
) => {
    const index =
        state.items.findIndex(
            (item) =>
                item.id === id,
        )

    if (index !== -1) {
        state.items.splice(
            index,
            1,
        )
    }
}

const showToast = (
    message: string,
    tone: ToastTone = 'info',
    duration = 4000,
) => {
    counter++

    const id = counter

    state.items.push({
        id,
        message,
        tone,
        duration,
    })

    if (duration > 0) {
        window.setTimeout(
            () => {
                removeToast(id)
            },
            duration,
        )
    }
}

export const useToast = () => {
    return {
        state,

        showToast,

        removeToast,

        success: (
            message: string,
        ) =>
            showToast(
                message,
                'success',
            ),

        error: (
            message: string,
        ) =>
            showToast(
                message,
                'error',
                5000,
            ),

        warning: (
            message: string,
        ) =>
            showToast(
                message,
                'warning',
                5000,
            ),

        info: (
            message: string,
        ) =>
            showToast(
                message,
                'info',
            ),
    }
}