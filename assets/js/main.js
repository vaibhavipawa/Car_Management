// Form validation and image upload handling
document.addEventListener('DOMContentLoaded', function() {
    // Image preview functionality
    const imageInput = document.getElementById('car-images');
    const imagePreview = document.getElementById('image-preview');
    
    if (imageInput) {
        imageInput.addEventListener('change', function() {
            imagePreview.innerHTML = '';
            if (this.files.length > 10) {
                alert('You can only upload up to 10 images');
                this.value = '';
                return;
            }
            
            for (let i = 0; i < this.files.length; i++) {
                const file = this.files[i];
                if (!file.type.startsWith('image/')) {
                    alert('Please upload only image files');
                    continue;
                }
                
                const reader = new FileReader();
                reader.onload = function(e) {
                    const img = document.createElement('img');
                    img.src = e.target.result;
                    img.className = 'preview-image';
                    imagePreview.appendChild(img);
                }
                reader.readAsDataURL(file);
            }
        });
    }

    // Search functionality
    const searchForm = document.getElementById('search-form');
    if (searchForm) {
        searchForm.addEventListener('submit', function(e) {
            e.preventDefault();
            const searchTerm = document.getElementById('search').value;
            window.location.href = `car_list.php?search=${encodeURIComponent(searchTerm)}`;
        });
    }

    // Delete confirmation
    const deleteButtons = document.querySelectorAll('.delete-car');
    deleteButtons.forEach(button => {
        button.addEventListener('click', function(e) {
            if (!confirm('Are you sure you want to delete this car?')) {
                e.preventDefault();
            }
        });
    });

    // Tag input handling
    const tagInput = document.getElementById('tag-input');
    const tagContainer = document.getElementById('tag-container');
    const tagsHidden = document.getElementById('tags');
    
    if (tagInput) {
        let tags = [];
        
        tagInput.addEventListener('keydown', function(e) {
            if (e.key === 'Enter' || e.key === ',') {
                e.preventDefault();
                const tag = this.value.trim();
                if (tag && !tags.includes(tag)) {
                    tags.push(tag);
                    updateTags();
                }
                this.value = '';
            }
        });

        function updateTags() {
            tagContainer.innerHTML = '';
            tags.forEach(tag => {
                const span = document.createElement('span');
                span.className = 'tag';
                span.textContent = tag;
                
                const removeBtn = document.createElement('button');
                removeBtn.textContent = '×';
                removeBtn.onclick = function() {
                    tags = tags.filter(t => t !== tag);
                    updateTags();
                };
                
                span.appendChild(removeBtn);
                tagContainer.appendChild(span);
            });
            tagsHidden.value = JSON.stringify(tags);
        }
    }
});
