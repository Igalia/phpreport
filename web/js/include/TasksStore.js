/*
 * Copyright (C) 2012 Igalia, S.L. <info@igalia.com>
 *
 * This file is part of PhpReport.
 *
 * PhpReport is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * PhpReport is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with PhpReport.  If not, see <http://www.gnu.org/licenses/>.
 */


/**
 * @class Ext.ux.TasksStore
 * @extends Ext.data.Store
 */
Ext.ux.TasksStore = Ext.extend(Ext.data.Store, {

    /**
     * List with the destroy, create and update operations pending to be sent to
     * the server.
     */
    pendingOperations: {},
    pendingOperationsTotalLength: 0,

    /**
     * Overwritten save method. The code is exactly the same but it makese sure
     * that the calls to services are sent in this order: delete, update, save.
     */
    save : function() {
        if (!this.writer) {
            throw new Ext.data.Store.Error('writer-undefined');
        }

        var queue = [],
            len,
            trans,
            batch,
            data = {},
            i;

        if(this.removed.length){
            queue.push(['destroy', this.removed]);
        }


        var rs = [].concat(this.getModifiedRecords());
        if(rs.length){

            var phantoms = [];
            for(i = rs.length-1; i >= 0; i--){
                if(rs[i].phantom === true){
                    var rec = rs.splice(i, 1).shift();
                    if(rec.isValid()){
                        phantoms.push(rec);
                    }
                }else if(!rs[i].isValid()){
                    rs.splice(i,1);
                }
            }

            if(phantoms.length){
                queue.push(['create', phantoms]);
            }


            if(rs.length){
                queue.push(['update', rs]);
            }
        }
        len = queue.length;
        if(len){
            batch = ++this.batchCounter;
            for(i = 0; i < len; ++i){
                trans = queue[i];
                this.pendingOperations[trans[0]] = trans[1];
            }
            if(this.fireEvent('beforesave', this, this.pendingOperations) !== false){
                pendingOperationsTotalLength = batch;
                this.processPendingOperations();
                return batch;
            }
        }
        return -1;
    },

    processPendingOperations: function () {

        if(this.pendingOperations['destroy'] != undefined) {
            this.doTransaction('destroy', this.pendingOperations['destroy'],
                    this.pendingOperationsTotalLength);
            delete this.pendingOperations['destroy'];
        }
        else if(this.pendingOperations['update'] != undefined) {
            this.doTransaction('update', this.pendingOperations['update'],
                    this.pendingOperationsTotalLength);
            delete this.pendingOperations['update'];
        }
        else if(this.pendingOperations['create'] != undefined) {
            this.doTransaction('create', this.pendingOperations['create'],
                    this.pendingOperationsTotalLength);
            delete this.pendingOperations['create'];
        }
    },

    /**
     * Callback processed when the destroy operation finishes.
     */
    onDestroyRecords: function (sucess, rs, data) {
        Ext.ux.TasksStore.superclass.onDestroyRecords.apply(this, arguments);
        this.processPendingOperations();
    },

    /**
     * Callback processed when the update operation finishes.
     */
    onUpdateRecords: function (sucess, rs, data) {
        Ext.ux.TasksStore.superclass.onUpdateRecords.apply(this, arguments);
        this.processPendingOperations();
    },
});
