

<?php $__env->startSection('content'); ?>
<div class="py-12">
    <div class="max-w-5xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6 text-gray-900">

                <h2 class="text-2xl font-bold mb-6">🔍 Drug Search</h2>

                
                <?php if(session('error')): ?>
                    <div class="mb-4 p-4 bg-red-100 text-red-700 rounded">
                        <?php echo e(session('error')); ?>

                    </div>
                <?php endif; ?>

                
                <form action="<?php echo e(route('drug.search.results')); ?>" method="GET" class="mb-6">
                    <div>
                        <label for="drug_name" class="block text-sm font-medium text-gray-700">Enter Drug Name</label>
                        <input type="text" name="drug_name" id="drug_name"
                            class="mt-1 w-full border border-gray-300 rounded-md shadow-sm p-2 focus:outline-none focus:ring-blue-500 focus:border-blue-500"
                            value="<?php echo e(old('drug_name', $drug_name ?? '')); ?>" required>
                    </div>
                    <button type="submit"
                        class="mt-4 inline-flex items-center px-4 py-2 bg-blue-600 text-white font-semibold rounded hover:bg-blue-700">
                        Search
                    </button>
                </form>

                
                <?php if(isset($searched) && collect($results)->isEmpty()): ?>
                    <div class="p-4 bg-yellow-100 text-yellow-800 rounded">
                        No drugs found for <strong><?php echo e($drug_name); ?></strong>.
                    </div>
                <?php elseif(isset($results) && collect($results)->isNotEmpty()): ?>
                    <h3 class="text-xl font-semibold mb-4">Search Results</h3>
                    <div class="overflow-x-auto">
                        <table class="min-w-full text-left border border-gray-300 rounded shadow-sm">
                            <thead class="bg-gray-100">
                                <tr>
                                    <th class="border px-4 py-2">RxCUI</th>
                                    <th class="border px-4 py-2">Name</th>
                                    <th class="border px-4 py-2">Base Names</th>
                                    <th class="border px-4 py-2">Dose Form</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white">
                                <?php $__currentLoopData = $results; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $drug): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <tr class="hover:bg-gray-50">
                                        <td class="border px-4 py-2"><?php echo e($drug['rxcui']); ?></td>
                                        <td class="border px-4 py-2"><?php echo e($drug['name']); ?></td>
                                        <td class="border px-4 py-2"><?php echo e(implode(', ', $drug['base_names']) ?: 'N/A'); ?></td>
                                        <td class="border px-4 py-2"><?php echo e($drug['dose_form_group'] ?? 'N/A'); ?></td>
                                    </tr>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </tbody>
                        </table>
                    </div>
                <?php endif; ?>

            </div>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\LaravelDemo\rxnorm-demo\resources\views/drugs/search.blade.php ENDPATH**/ ?>