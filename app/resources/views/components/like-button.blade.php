{{-- 
    いいねボタンコンポーネント（Ajax対応）
    @param \App\Models\Service $service
    @param bool $isLiked
    @param int $likeCount
--}}

<button
    type="button"
    class="like-button flex items-center space-x-1 py-0 px-4 rounded-full transition-colors duration-200 focus:outline-none"
    data-service-id="{{ $service->id }}"
>
    <svg class="w-6 h-6 transform transition-all duration-300 like-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
              d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-.318-.318a4.5 4.5 0 00-6.364 0z" />
    </svg>
    <span class="like-count text-sm font-semibold">{{ $likeCount }}</span>
</button>

<script>
document.addEventListener('DOMContentLoaded', function () {
    document.querySelectorAll('.like-button').forEach(function(button){
        const iconPath = button.querySelector('path');
        const countEl = button.querySelector('.like-count');
        let isLiked = {{ $isLiked ? 'true' : 'false' }};
        
        // 初期表示
        if(isLiked){
            iconPath.style.fill = '#ef4444';
            iconPath.style.stroke = '#ef4444';
            countEl.style.color = '#ef4444'; 
        } else {
            iconPath.style.fill = 'transparent';
            iconPath.style.stroke = '#6b7280';
            countEl.style.color = '#6b7280';
        }

        button.addEventListener('click', function(){
            const serviceId = this.dataset.serviceId;

            fetch(`/services/${serviceId}/like`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Content-Type': 'application/json',
                    'Accept': 'application/json'
                },
                body: JSON.stringify({})
            })
            .then(response => response.json())
            .then(data => {
                if(data.success){
                    isLiked = data.isLiked;

                    if(isLiked){
                        iconPath.style.fill = '#ef4444';
                        iconPath.style.stroke = '#ef4444';
                        countEl.style.color = '#ef4444';  
                    } else {
                        iconPath.style.fill = 'transparent';
                        iconPath.style.stroke = '#6b7280';
                        countEl.style.color = '#6b7280';
                    }

                    countEl.textContent = data.likeCount;
                }
            })
            .catch(error => console.error(error));
        });
    });
});
</script>