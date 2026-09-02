import {
    onMounted,
    onUnmounted,
    ref,
} from 'vue'

export function usePolling<T>(
    url: string,
    onData: (data: T) => void,
    interval = 2000,
) {
    const loading = ref(false)

    let timer: number | null = null

    const poll = async () => {
        /*
         * Jangan polling ketika tab browser
         * sedang tidak aktif.
         */
        if (
            document.hidden ||
            loading.value
        ) {
            return
        }

        loading.value = true

        try {
            const response = await fetch(url, {
                method: 'GET',

                headers: {
                    Accept: 'application/json',
                },

                credentials: 'same-origin',
            })

            if (!response.ok) {
                return
            }

            const data =
                (await response.json()) as T

            onData(data)
        } finally {
            loading.value = false
        }
    }

    const start = () => {
        void poll()

        timer = window.setInterval(
            () => {
                void poll()
            },
            interval,
        )
    }

    const stop = () => {
        if (timer !== null) {
            window.clearInterval(timer)
            timer = null
        }
    }

    onMounted(start)
    onUnmounted(stop)

    return {
        poll,
        stop,
        loading,
    }
}