<!-- Attendance Modal -->
<div id="attendanceModal" class="hidden fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl shadow-2xl max-w-md w-full transform transition-all">
        <!-- Modal Header -->
        <div class="p-6 border-b border-gray-100">
            <div class="flex items-center justify-between">
                <div>
                    <h3 class="text-xl font-bold text-gray-800">Mark Attendance</h3>
                    <p class="text-sm text-gray-500 mt-1" id="modalScheduleInfo"></p>
                </div>
                <button onclick="closeAttendanceModal()" class="text-gray-400 hover:text-gray-600 transition">
                    <i class="fa-solid fa-times text-xl"></i>
                </button>
            </div>
        </div>

        <!-- Modal Body -->
        <form id="attendanceForm" class="p-6 space-y-6">
            <input type="hidden" id="scheduleId" name="schedule_id">
            <input type="hidden" id="studentId" name="student_id">

            <!-- Attendance Checkboxes -->
            <div class="space-y-4">
                <div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg hover:bg-gray-100 transition">
                    <div class="flex items-center space-x-3">
                        <div class="w-10 h-10 bg-gradient-to-br from-blue-500 to-indigo-600 rounded-full flex items-center justify-center">
                            <i class="fa-solid fa-chalkboard-teacher text-white"></i>
                        </div>
                        <div>
                            <label for="teacherPresent" class="font-semibold text-gray-800 cursor-pointer">Teacher Present</label>
                            <p class="text-xs text-gray-500">Mark yourself as present</p>
                        </div>
                    </div>
                    <label class="relative inline-flex items-center cursor-pointer">
                        <input type="hidden" name="teacher_present" value="0">
                        <input type="checkbox" id="teacherPresent" name="teacher_present" value="1" class="sr-only peer">
                        <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-blue-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-blue-600"></div>
                    </label>
                </div>

                <div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg hover:bg-gray-100 transition">
                    <div class="flex items-center space-x-3">
                        <div class="w-10 h-10 bg-gradient-to-br from-green-500 to-emerald-600 rounded-full flex items-center justify-center">
                            <i class="fa-solid fa-user-graduate text-white"></i>
                        </div>
                        <div>
                            <label for="studentPresent" class="font-semibold text-gray-800 cursor-pointer">Student Present</label>
                            <p class="text-xs text-gray-500" id="studentName"></p>
                        </div>
                    </div>
                    <label class="relative inline-flex items-center cursor-pointer">
                        <input type="hidden" name="student_present" value="0">
                        <input type="checkbox" id="studentPresent" name="student_present" value="1" class="sr-only peer">
                        <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-green-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-green-600"></div>
                    </label>
                </div>
            </div>

            <!-- Remark Field -->
            <div>
                <label for="remark" class="block text-sm font-semibold text-gray-700 mb-2">
                    Remark <span class="text-red-500" id="remarkRequired" style="display: none;">*</span>
                </label>
                <textarea 
                    id="remark" 
                    name="remark" 
                    rows="4" 
                    class="w-full px-4 py-3 border border-gray-200 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent resize-none"
                    placeholder="Add any notes or remarks about this session..."></textarea>
                <p class="text-xs text-gray-500 mt-2" id="remarkHint">
                    <i class="fa-solid fa-info-circle mr-1"></i>Remark is required when marking an absence
                </p>
            </div>

            <!-- Error Message -->
            <div id="attendanceError" class="hidden p-4 bg-red-50 border border-red-200 rounded-lg">
                <div class="flex items-start space-x-3">
                    <i class="fa-solid fa-exclamation-circle text-red-500 mt-0.5"></i>
                    <div class="flex-1">
                        <p class="text-sm font-semibold text-red-800">Error</p>
                        <p class="text-sm text-red-600" id="attendanceErrorMessage"></p>
                    </div>
                </div>
            </div>

            <!-- Modal Footer -->
            <div class="flex items-center space-x-3 pt-4 border-t border-gray-100">
                <button 
                    type="button" 
                    onclick="closeAttendanceModal()" 
                    class="flex-1 px-4 py-3 bg-gray-100 text-gray-700 rounded-lg font-semibold hover:bg-gray-200 transition">
                    Cancel
                </button>
                <button 
                    type="submit" 
                    id="submitAttendance"
                    class="flex-1 px-4 py-3 bg-gradient-to-r from-vibrant-green to-deep-blue text-white rounded-lg font-semibold hover:shadow-lg transition">
                    <span id="submitText">Save Attendance</span>
                    <span id="submitLoading" class="hidden">
                        <i class="fa-solid fa-spinner fa-spin mr-2"></i>Saving...
                    </span>
                </button>
            </div>
        </form>
    </div>
</div>

<script>
let currentSchedule = null;

function openAttendanceModal(schedule) {
    currentSchedule = schedule;
    
    // Set schedule info
    document.getElementById('scheduleId').value = schedule.id;
    document.getElementById('studentId').value = schedule.student.id;
    document.getElementById('modalScheduleInfo').textContent = 
        `${schedule.course.name} - ${schedule.starts_at_formatted}`;
    document.getElementById('studentName').textContent = schedule.student.name;
    
    // Reset form
    document.getElementById('attendanceForm').reset();
    document.getElementById('scheduleId').value = schedule.id;
    document.getElementById('studentId').value = schedule.student.id;
    document.getElementById('attendanceError').classList.add('hidden');
    
    // Pre-fill if attendance exists
    if (schedule.attendance) {
        document.getElementById('teacherPresent').checked = schedule.attendance.teacher_present;
        document.getElementById('studentPresent').checked = schedule.attendance.student_present;
        document.getElementById('remark').value = schedule.attendance.remark || '';
    }
    
    // Show modal
    document.getElementById('attendanceModal').classList.remove('hidden');
    updateRemarkRequirement();
}

function closeAttendanceModal() {
    document.getElementById('attendanceModal').classList.add('hidden');
    currentSchedule = null;
}

function updateRemarkRequirement() {
    const teacherPresent = document.getElementById('teacherPresent').checked;
    const studentPresent = document.getElementById('studentPresent').checked;
    const remarkRequired = document.getElementById('remarkRequired');
    const remarkHint = document.getElementById('remarkHint');
    
    if (!teacherPresent || !studentPresent) {
        remarkRequired.style.display = 'inline';
        remarkHint.classList.remove('text-gray-500');
        remarkHint.classList.add('text-red-600', 'font-semibold');
    } else {
        remarkRequired.style.display = 'none';
        remarkHint.classList.remove('text-red-600', 'font-semibold');
        remarkHint.classList.add('text-gray-500');
    }
}

// Listen for checkbox changes
document.addEventListener('DOMContentLoaded', function() {
    const teacherCheckbox = document.getElementById('teacherPresent');
    const studentCheckbox = document.getElementById('studentPresent');
    
    if (teacherCheckbox) {
        teacherCheckbox.addEventListener('change', updateRemarkRequirement);
    }
    if (studentCheckbox) {
        studentCheckbox.addEventListener('change', updateRemarkRequirement);
    }
});

// Handle form submission
document.getElementById('attendanceForm').addEventListener('submit', async function(e) {
    e.preventDefault();
    
    const teacherPresent = document.getElementById('teacherPresent').checked;
    const studentPresent = document.getElementById('studentPresent').checked;
    const remark = document.getElementById('remark').value.trim();
    
    // Client-side validation
    if ((!teacherPresent || !studentPresent) && !remark) {
        showError('Remark is required when marking an absence.');
        return;
    }
    
    // Show loading state
    document.getElementById('submitText').classList.add('hidden');
    document.getElementById('submitLoading').classList.remove('hidden');
    document.getElementById('submitAttendance').disabled = true;
    
    try {
        const formData = new FormData(this);
        
        const response = await fetch('{{ route("teacher.attendance.store") }}', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json',
            },
            body: formData
        });
        
        const data = await response.json();
        
        if (data.success) {
            // Close modal
            closeAttendanceModal();
            
            // Show success message
            showSuccessMessage('Attendance marked successfully!');
            
            // Reload page to update attendance display
            setTimeout(() => {
                window.location.reload();
            }, 1000);
        } else {
            showError(data.message || 'Failed to save attendance.');
        }
    } catch (error) {
        showError('An error occurred. Please try again.');
        console.error('Error:', error);
    } finally {
        // Reset loading state
        document.getElementById('submitText').classList.remove('hidden');
        document.getElementById('submitLoading').classList.add('hidden');
        document.getElementById('submitAttendance').disabled = false;
    }
});

function showError(message) {
    document.getElementById('attendanceErrorMessage').textContent = message;
    document.getElementById('attendanceError').classList.remove('hidden');
}

function showSuccessMessage(message) {
    // Create a toast notification
    const toast = document.createElement('div');
    toast.className = 'fixed top-4 right-4 bg-green-500 text-white px-6 py-4 rounded-lg shadow-lg z-50 flex items-center space-x-3 animate-fade-in';
    toast.innerHTML = `
        <i class="fa-solid fa-check-circle text-xl"></i>
        <span class="font-semibold">${message}</span>
    `;
    document.body.appendChild(toast);
    
    setTimeout(() => {
        toast.remove();
    }, 3000);
}

// Close modal on escape key
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        closeAttendanceModal();
    }
});

// Close modal on background click
document.getElementById('attendanceModal').addEventListener('click', function(e) {
    if (e.target === this) {
        closeAttendanceModal();
    }
});
</script>
